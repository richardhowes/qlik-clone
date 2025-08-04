import { queryParams, type QueryParams } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\QueryController::saved
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
export const saved = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: saved.url(args, options),
    method: 'get',
})

saved.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}/query/{query}',
}

/**
* @see \App\Http\Controllers\QueryController::saved
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
saved.url = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (Array.isArray(args)) {
        args = {
            data_source: args[0],
            query: args[1],
        }
    }

    const parsedArgs = {
        data_source: args.data_source,
        query: typeof args.query === 'object'
        ? args.query.id
        : args.query,
    }

    return saved.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace('{query}', parsedArgs.query.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::saved
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
saved.get = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: saved.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::saved
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
saved.head = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: saved.url(args, options),
    method: 'head',
})

const editor = {
    saved,
}

export default editor