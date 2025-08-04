import { queryParams, type QueryParams } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AiTestController::test
* @see app/Http/Controllers/AiTestController.php:12
* @route '/ai/test'
*/
export const test = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: test.url(options),
    method: 'get',
})

test.definition = {
    methods: ['get','head'],
    url: '/ai/test',
}

/**
* @see \App\Http\Controllers\AiTestController::test
* @see app/Http/Controllers/AiTestController.php:12
* @route '/ai/test'
*/
test.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return test.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiTestController::test
* @see app/Http/Controllers/AiTestController.php:12
* @route '/ai/test'
*/
test.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: test.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AiTestController::test
* @see app/Http/Controllers/AiTestController.php:12
* @route '/ai/test'
*/
test.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: test.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AiTestController::generate
* @see app/Http/Controllers/AiTestController.php:17
* @route '/ai/generate'
*/
export const generate = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ['post'],
    url: '/ai/generate',
}

/**
* @see \App\Http\Controllers\AiTestController::generate
* @see app/Http/Controllers/AiTestController.php:17
* @route '/ai/generate'
*/
generate.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiTestController::generate
* @see app/Http/Controllers/AiTestController.php:17
* @route '/ai/generate'
*/
generate.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: generate.url(options),
    method: 'post',
})

const ai = {
    test,
    generate,
}

export default ai