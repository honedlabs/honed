<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Delimiter
    |--------------------------------------------------------------------------
    |
    | You can specify the delimiter to be used when parsing a query parameter as
    | an array.
    |
    */

    'delimiter' => ',',

    /*
    |--------------------------------------------------------------------------
    | Strictness
    |--------------------------------------------------------------------------
    |
    | You can specify if the filter should be strict about the options that are
    | allowed. If true, only the options provided will be allowed. If false,
    | the filter will allow any options that are provided even if not explicitly
    | provided.
    |
    */

    'strict' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable matches
    |--------------------------------------------------------------------------
    |
    | You can enable or disable the matches feature, which allows your users to
    | select which columns they want to use to execute a search on the query.
    |
    | Enabling this will also provide a 'searches' property when serialized to
    | allow you to bind the options to a form input.
    |
    */

    'match' => false,

    /*
    |--------------------------------------------------------------------------
    | Query parameter keys
    |--------------------------------------------------------------------------
    |
    | You can modify the query parameters that are used to refine the query
    | if not supplied at the refiner level. If your refiner is scoped, these
    | will be prefixed with the scope name.
    |
    */

    /** The parameter name for the sort field and direction. */
    'sort_key' => 'sort',

    /** The parameter name when using a text search. */
    'search_key' => 'search',

    /** The parameter name when selecting which columns to match on. */
    'match_key' => 'match',

];
