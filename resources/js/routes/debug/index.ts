import { queryParams, type QueryParams } from './../../wayfinder'
/**
* @see \App\Http\Controllers\DebugController::schema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
export const schema = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: schema.url(options),
    method: 'get',
})

schema.definition = {
    methods: ['get','head'],
    url: '/debug/schema',
}

/**
* @see \App\Http\Controllers\DebugController::schema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
schema.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return schema.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DebugController::schema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
schema.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: schema.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DebugController::schema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
schema.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: schema.url(options),
    method: 'head',
})

const debug = {
    schema,
}

export default debug