<?php

declare(strict_types=1);

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

    'endpoint' => '/table',

    /*
    |--------------------------------------------------------------------------
    | Serialize
    |--------------------------------------------------------------------------
    |
    | By default, the table will not serialize the model attributes and opt to
    | build the record up using the provided columns. If you would like to
    | serialize the model attributes, and then use the columns to override the
    | properties, you can set this to true.
    |
    */

    'serialize' => false,

    /*
    |--------------------------------------------------------------------------
    | Select
    |--------------------------------------------------------------------------
    |
    | By default, the table will not select any columns. If you would like to
    | globally enable column selection, you can set this to true.
    |
    */

    'select' => false,

    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    |
    | You can specify the default empty state to be used when no results are
    | found. This will be used as the default empty state for all tables.
    |
    */

    'empty_state' => [
        /** Specify the default title to display if the table is empty. */
        'title' => 'No results found',

        /** Specify the default message to display if the table is empty. */
        'message' => 'There are no results to display.',

        /** Specify an icon to display in the empty state. */
        'icon' => null,

        /** Specify a message to display if the table is empty due to refining. */
        'refining' => 'There are no results matching your filters.',
    ],

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
    'page_key' => 'page',

    /** The number of links to show either side of the current page. */
    'window' => 2,

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
    'column_key' => 'cols',

    /** The parameter name for the number of records to display. */
    'record_key' => 'rows',

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
    'sort_key' => 'sort',

    /** The parameter name when using a text search. */
    'search_key' => 'search',

    /** The parameter name when selecting which columns to match on. */
    'match_key' => 'match',

    /*
    |--------------------------------------------------------------------------
    | Empty state
    |--------------------------------------------------------------------------
    |
    | You can enable or disable the matches feature, which allows your users to
    | select which columns they want to use to execute a search on the query.
    |
    | Enabling this will also provide a 'searches' property when serialized to
    | allow you to bind the options to a form input.
    |
    */

    'exporter' => \Honed\Table\Exports\TableExport::class,
];
