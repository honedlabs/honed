<?php

declare(strict_types=1);

namespace Honed\Honed\Commands;

use Honed\Command\Commands\ResponseMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;
use function trim;

#[AsCommand(name: 'make:inertia-response')]
class InertiaResponseMakeCommand extends ResponseMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:inertia-response';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $type = $this->getType();

        if ($type) {
            return $this->resolveStubPath('/stubs/honed.'.$type.'.response.stub');
        }

        return $this->resolveStubPath('/stubs/honed.response.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../..'.$stub;
    }

    /**
     * Get the console command options
     *
     * @return array<int,array<int,mixed>>
     */
    protected function getOptions(): array
    {
        return [
            ...parent::getOptions(),
            ['type', 't', InputOption::VALUE_OPTIONAL, 'The type of the response to create'],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string,mixed>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => [
                'What should the '.mb_strtolower($this->type).' be named?',
                'E.g. IndexUserResponse',
            ],
        ];
    }

    /**
     * Perform types after the user was prompted for missing arguments.
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        $types = array_map(
            static fn (string $type) => ucfirst($type),
            $this->getAvailableTypes()
        );

        $input->setOption('type', select(
            'What type should the '.mb_strtolower($this->type).' handle?',
            [...$types, null => 'None'],
            hint: 'If no type is provided, the default response stub will be used.'
        ));
    }

    /**
     * Get the type of response to create
     */
    protected function getType(): ?string
    {
        $input = $this->option('type');

        if (in_array($input, $this->getAvailableTypes())) {
            return $input;
        }

        return null;
    }

    /**
     * Get the available types.
     *
     * @return array<int, string>
     */
    protected function getAvailableTypes(): array
    {
        return ['index', 'show', 'create', 'edit', 'delete'];
    }
}
