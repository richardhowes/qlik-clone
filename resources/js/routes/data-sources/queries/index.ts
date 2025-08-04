import { queryParams, type QueryParams } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
export const index = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}/queries',
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
index.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { data_source: args }
    }

    if (Array.isArray(args)) {
        args = {
            data_source: args[0],
        }
    }

    const parsedArgs = {
        data_source: args.data_source,
    }

    return index.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
index.get = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
index.head = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: index.url(args, options),
    method: 'head',
})

const queries = {
    index,
}

export default queries