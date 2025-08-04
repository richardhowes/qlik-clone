import { queryParams, type QueryParams } from './../../wayfinder'
/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
export const index = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ['get','head'],
    url: '/queries',
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
index.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
index.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
index.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: index.url(options),
    method: 'head',
})

const queries = {
    index,
}

export default queries