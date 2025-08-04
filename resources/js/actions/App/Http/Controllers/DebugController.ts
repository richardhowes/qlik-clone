import { queryParams, type QueryParams } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DebugController::checkSchema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
export const checkSchema = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: checkSchema.url(options),
    method: 'get',
})

checkSchema.definition = {
    methods: ['get','head'],
    url: '/debug/schema',
}

/**
* @see \App\Http\Controllers\DebugController::checkSchema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
checkSchema.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return checkSchema.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DebugController::checkSchema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
checkSchema.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: checkSchema.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DebugController::checkSchema
* @see app/Http/Controllers/DebugController.php:11
* @route '/debug/schema'
*/
checkSchema.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: checkSchema.url(options),
    method: 'head',
})

const DebugController = { checkSchema }

export default DebugController