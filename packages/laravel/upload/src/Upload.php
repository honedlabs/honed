<?php

declare(strict_types=1);

namespace Honed\Upload;

use Aws\S3\PostObjectV4;
use Honed\Core\Concerns\HasRequest;
use Honed\Core\Primitive;
use Honed\Upload\Concerns\DispatchesPresignEvents;
use Honed\Upload\Concerns\HasFilePath;
use Honed\Upload\Concerns\ValidatesUpload;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Number;

class Upload extends Primitive implements Responsable
{
    use DispatchesPresignEvents;
    use HasFilePath;
    use HasRequest;
    use ValidatesUpload;

    /**
     * The upload data to use from the request.
     *
     * @var \Honed\Upload\UploadData|null
     */
    protected $data;

    /**
     * Get the configuration rules for validating file uploads.
     *
     * @var array<int, \Honed\Upload\UploadRule>
     */
    protected $rules = [];

    /**
     * The access control list to use for the file.
     *
     * @var string|null
     */
    protected $acl;

    /**
     * The additional data to return with the presign response.
     *
     * @var mixed
     */
    protected $returns = null;

    /**
     * Whether the upload accepts multiple files.
     *
     * @var bool
     */
    protected $multiple = false;

    /**
     * Whether to only return the upload message.
     *
     * @var bool
     */
    protected $message = false;

    /**
     * Create a new upload instance.
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request($request);
    }

    /**
     * Create a new upload instance.
     *
     * @param  string|null  $disk
     * @return static
     */
    public static function make($disk = null)
    {
        return resolve(static::class)
            ->disk($disk);
    }

    /**
     * Create a new upload instance for the given disk.
     *
     * @param  string  $disk
     * @return static
     */
    public static function into($disk)
    {
        return static::make($disk);
    }

    /**
     * Set the rules for validating file uploads.
     *
     * @param  iterable<\Honed\Upload\UploadRule>  ...$rules
     * @return $this
     */
    public function rules(...$rules)
    {
        $rules = Arr::flatten($rules);

        $this->rules = \array_merge($this->rules, $rules);

        return $this;
    }

    /**
     * Get the rules for validating file uploads.
     *
     * @return array<int, \Honed\Upload\UploadRule>
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set the access control list to use for the file.
     *
     * @param  string  $acl
     * @return $this
     */
    public function acl($acl)
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * Get the access control list to use for the file.
     *
     * @return string
     */
    public function getACL()
    {
        return $this->acl ?? static::getDefaultACL();
    }

    /**
     * Get the default access control list to use for the file.
     *
     * @return string
     */
    public static function getDefaultACL()
    {
        return type(config('upload.acl', 'public-read'))->asString();
    }

    /**
     * Set additiional data to return with the presign response.
     *
     * @param  mixed  $return
     * @return $this
     */
    public function shouldReturn($return)
    {
        $this->returns = $return;

        return $this;
    }

    /**
     * Get the additional data to return with the presign response.
     *
     * @return mixed
     */
    public function getReturns()
    {
        return $this->evaluate($this->returns);
    }

    /**
     * Set whether the upload accepts multiple files.
     *
     * @param  bool  $multiple
     * @return $this
     */
    public function multiple($multiple = true)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Determine whether the upload accepts multiple files.
     *
     * @return bool
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set whether to only return the upload message.
     *
     * @param  bool  $message
     * @return $this
     */
    public function message($message = true)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Determine whether to only return the upload message.
     *
     * @return bool
     */
    public function onlyMessage()
    {
        return $this->message;
    }

    /**
     * Create the upload message.
     *
     * @return string
     */
    public function getMessage()
    {
        $extensions = $this->getExtensions();
        $mimes = $this->getMimeTypes();

        $numMimes = \count($mimes);
        $numExts = \count($extensions);

        $typed = match (true) {
            $numExts > 0 && $numExts < 4 => \implode(', ', \array_map(
                static fn ($ext) => \mb_strtoupper(\trim($ext)),
                $extensions
            )),

            $numMimes > 0 && $numMimes < 4 => \ucfirst(\implode(', ', \array_map(
                static fn ($mime) => \trim($mime, ' /'),
                $mimes
            ))),

            $this->isMultiple() => 'Files',

            default => 'A single file',
        };

        return $typed.' up to '.Number::fileSize($this->getMax());
    }

    /**
     * Get the S3 bucket to use for uploading files.
     *
     * @return string
     */
    public function getBucket()
    {
        $disk = $this->getDisk();

        return type(config("filesystems.disks.{$disk}.bucket"))->asString();
    }

    /**
     * Get the defaults for form input fields.
     *
     * @param  string  $key
     * @return array<string,mixed>
     */
    public function getFormInputs($key)
    {
        return [
            'acl' => $this->getACL(),
            'key' => $key,
        ];
    }

    /**
     * Get the policy condition options for the request.
     *
     * @param  string  $key
     * @return array<int,array<string|int,mixed>>
     */
    public function getOptions($key)
    {
        $options = [
            ['eq', '$acl', $this->getACL()],
            ['eq', '$key', $key],
            ['eq', '$bucket', $this->getBucket()],
            ['content-length-range', $this->getMin(), $this->getMax()],
        ];

        $mimes = $this->getMimeTypes();

        if (filled($mimes)) {
            $options[] = ['starts-with', '$Content-Type', \implode(',', $mimes)];
        }

        return $options;
    }

    /**
     * Validate the incoming request.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return array{\Honed\Upload\UploadData, \Honed\Upload\UploadRule|null}
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($request)
    {
        $request ??= $this->getRequest();

        [$name, $extension] =
            static::destructureFilename($request->input('name'));

        $request->merge([
            'name' => $name,
            'extension' => $extension,
        ])->all();

        $rule = Arr::first(
            $this->getRules(),
            static fn (UploadRule $rule) => $rule->isMatching(
                $request->input('type'),
                $extension,
            ),
        );

        $validated = Validator::make(
            $request->all(),
            $rule?->createRules() ?? $this->createRules(),
            [],
            $this->getAttributes(),
        )->validate();

        return [UploadData::from($validated), $rule];
    }

    /**
     * Get the attributes for the validator.
     *
     * @return array<string,string>
     */
    public function getAttributes()
    {
        return [
            'name' => 'file name',
            'extension' => 'file extension',
            'type' => 'file type',
            'size' => 'file size',
        ];
    }

    /**
     * Create a presigned POST URL using.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return array{attributes:array<string,mixed>,inputs:array<string,mixed>}
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create($request = null)
    {
        [$data, $rule] = $this->validate($request);

        $this->data = $data;

        $key = $this->createKey($data);

        $postObject = new PostObjectV4(
            $this->getClient(),
            $this->getBucket(),
            $this->getFormInputs($key),
            $this->getOptions($key),
            $this->formatExpiry($rule ? $rule->getExpiry() : $this->getExpiry())
        );

        return [
            'attributes' => $postObject->getFormAttributes(),
            'inputs' => $postObject->getFormInputs(),
            'data' => $this->getReturns(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $data = [
            'multiple' => $this->isMultiple(),
            'message' => $this->getMessage(),
        ];

        if ($this->onlyMessage()) {
            return $data;
        }

        return \array_merge($data, [
            'extensions' => $this->getExtensions(),
            'mimes' => $this->getMimeTypes(),
            'size' => $this->getMax(),
        ]);
    }

    /**
     * Create a response for the upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        $presign = $this->create($request);

        return response()->json($presign);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        $data = $this->data;

        return match ($parameterName) {
            'data' => [$data],
            'key' => [$data ? $this->createKey($data) : null],
            'file' => [$data ? $this->createFilename($data).'.'.$data->extension : null],
            'filename' => [$data ? $this->createFilename($data) : null],
            'bucket' => [$this->getBucket()],
            'name' => [$data?->name],
            'extension' => [$data?->extension],
            'type' => [$data?->type],
            'size' => [$data?->size],
            'meta' => [$data?->meta],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        if ($parameterType === UploadData::class && isset($this->data)) {
            return [$this->data];
        }

        return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
    }
}
