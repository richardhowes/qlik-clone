import { queryParams, type QueryParams } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AiInsightsController::ask
* @see app/Http/Controllers/AiInsightsController.php:50
* @route '/insights/ask'
*/
export const ask = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: ask.url(options),
    method: 'post',
})

ask.definition = {
    methods: ['post'],
    url: '/insights/ask',
}

/**
* @see \App\Http\Controllers\AiInsightsController::ask
* @see app/Http/Controllers/AiInsightsController.php:50
* @route '/insights/ask'
*/
ask.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return ask.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::ask
* @see app/Http/Controllers/AiInsightsController.php:50
* @route '/insights/ask'
*/
ask.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: ask.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AiInsightsController::proactive
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
export const proactive = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: proactive.url(options),
    method: 'get',
})

proactive.definition = {
    methods: ['get','head'],
    url: '/insights/proactive',
}

/**
* @see \App\Http\Controllers\AiInsightsController::proactive
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
proactive.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return proactive.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::proactive
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
proactive.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: proactive.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AiInsightsController::proactive
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
proactive.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: proactive.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AiInsightsController::visualization
* @see app/Http/Controllers/AiInsightsController.php:202
* @route '/insights/visualization'
*/
export const visualization = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: visualization.url(options),
    method: 'post',
})

visualization.definition = {
    methods: ['post'],
    url: '/insights/visualization',
}

/**
* @see \App\Http\Controllers\AiInsightsController::visualization
* @see app/Http/Controllers/AiInsightsController.php:202
* @route '/insights/visualization'
*/
visualization.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return visualization.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::visualization
* @see app/Http/Controllers/AiInsightsController.php:202
* @route '/insights/visualization'
*/
visualization.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: visualization.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AiInsightsController::save
* @see app/Http/Controllers/AiInsightsController.php:231
* @route '/insights/save'
*/
export const save = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: save.url(options),
    method: 'post',
})

save.definition = {
    methods: ['post'],
    url: '/insights/save',
}

/**
* @see \App\Http\Controllers\AiInsightsController::save
* @see app/Http/Controllers/AiInsightsController.php:231
* @route '/insights/save'
*/
save.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return save.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::save
* @see app/Http/Controllers/AiInsightsController.php:231
* @route '/insights/save'
*/
save.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: save.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AiInsightsController::debug
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
export const debug = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: debug.url(options),
    method: 'get',
})

debug.definition = {
    methods: ['get','head'],
    url: '/insights/debug',
}

/**
* @see \App\Http\Controllers\AiInsightsController::debug
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
debug.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return debug.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::debug
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
debug.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: debug.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AiInsightsController::debug
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
debug.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: debug.url(options),
    method: 'head',
})

const insights = {
    ask,
    proactive,
    visualization,
    save,
    debug,
}

export default insights