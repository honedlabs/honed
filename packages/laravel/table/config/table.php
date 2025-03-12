<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------------
    |
    | You can specify a global endpoint to handle all table action requests
    | when using the provided Route macro. This will be used as the default
    | endpoint for all tables.
    |
    */

    'endpoint' => '/actions',

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
    | Pagination
    |--------------------------------------------------------------------------
    |
    | You can specify how the tables should be paginated by default. This will
    | include how they are paginated, the number of records per page, and the
    | default number of records per page if this is an array which allows for
    | dynamic pagination.
    |
    */

    /** 'length-aware' or 'cursor' or 'simple' or 'collection' */
    'paginator' => 'length-aware',

    /** The options for the table, int or array of ints */
    'pagination' => 10,

    /** This should be a value in the provided options */
    'default_pagination' => 10,

    /** The parameter name for the page number. */
    'pages_key' => 'page',

    /*
    |--------------------------------------------------------------------------
    | Toggleable
    |--------------------------------------------------------------------------
    |
    | The package supports toggleable tables, which allows your users to toggle
    | the visibility of columns. This feature can be enabled or disabled
    | globally, and the package will respect the user's preferences when
    | rendering the table.
    |
    */

    /** Whether all tables should be toggleable */
    'toggle' => false,

    /** Whether all tables should have cookies enabled */
    'remember' => false,

    /** How long the cookie should be stored for, this is 1 year */
    'duration' => 15768000,

    /** The parameter name for the columns to display. */
    'columns_key' => 'cols',

    /** The parameter name for the number of records to display. */
    'records_key' => 'rows',

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
    | if not supplied at the table level. If your table is scoped, these
    | will be prefixed with the scope name.
    |
    */

    /** The parameter name for the sort field and direction. */
    'sorts_key' => 'sort',

    /** The parameter name when using a text search. */
    'searches_key' => 'search',

    /** The parameter name when selecting which columns to match on. */
    'matches_key' => 'match',
];
