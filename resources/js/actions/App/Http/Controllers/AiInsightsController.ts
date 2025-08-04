import { queryParams, type QueryParams } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AiInsightsController::index
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
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
    url: '/insights',
}

/**
* @see \App\Http\Controllers\AiInsightsController::index
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
*/
index.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::index
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
*/
index.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AiInsightsController::index
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
*/
index.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AiInsightsController::askQuestion
* @see app/Http/Controllers/AiInsightsController.php:50
* @route '/insights/ask'
*/
export const askQuestion = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: askQuestion.url(options),
    method: 'post',
})

askQuestion.definition = {
    methods: ['post'],
    url: '/insights/ask',
}

/**
* @see \App\Http\Controllers\AiInsightsController::askQuestion
* @see app/Http/Controllers/AiInsightsController.php:50
* @route '/insights/ask'
*/
askQuestion.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return askQuestion.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::askQuestion
* @see app/Http/Controllers/AiInsightsController.php:50
* @route '/insights/ask'
*/
askQuestion.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: askQuestion.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AiInsightsController::getProactiveInsights
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
export const getProactiveInsights = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: getProactiveInsights.url(options),
    method: 'get',
})

getProactiveInsights.definition = {
    methods: ['get','head'],
    url: '/insights/proactive',
}

/**
* @see \App\Http\Controllers\AiInsightsController::getProactiveInsights
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
getProactiveInsights.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return getProactiveInsights.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::getProactiveInsights
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
getProactiveInsights.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: getProactiveInsights.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AiInsightsController::getProactiveInsights
* @see app/Http/Controllers/AiInsightsController.php:170
* @route '/insights/proactive'
*/
getProactiveInsights.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: getProactiveInsights.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AiInsightsController::generateVisualization
* @see app/Http/Controllers/AiInsightsController.php:202
* @route '/insights/visualization'
*/
export const generateVisualization = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: generateVisualization.url(options),
    method: 'post',
})

generateVisualization.definition = {
    methods: ['post'],
    url: '/insights/visualization',
}

/**
* @see \App\Http\Controllers\AiInsightsController::generateVisualization
* @see app/Http/Controllers/AiInsightsController.php:202
* @route '/insights/visualization'
*/
generateVisualization.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return generateVisualization.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::generateVisualization
* @see app/Http/Controllers/AiInsightsController.php:202
* @route '/insights/visualization'
*/
generateVisualization.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: generateVisualization.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AiInsightsController::saveInsight
* @see app/Http/Controllers/AiInsightsController.php:231
* @route '/insights/save'
*/
export const saveInsight = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: saveInsight.url(options),
    method: 'post',
})

saveInsight.definition = {
    methods: ['post'],
    url: '/insights/save',
}

/**
* @see \App\Http\Controllers\AiInsightsController::saveInsight
* @see app/Http/Controllers/AiInsightsController.php:231
* @route '/insights/save'
*/
saveInsight.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return saveInsight.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::saveInsight
* @see app/Http/Controllers/AiInsightsController.php:231
* @route '/insights/save'
*/
saveInsight.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: saveInsight.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AiInsightsController::debugDataSource
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
export const debugDataSource = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: debugDataSource.url(options),
    method: 'get',
})

debugDataSource.definition = {
    methods: ['get','head'],
    url: '/insights/debug',
}

/**
* @see \App\Http\Controllers\AiInsightsController::debugDataSource
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
debugDataSource.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return debugDataSource.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::debugDataSource
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
debugDataSource.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: debugDataSource.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AiInsightsController::debugDataSource
* @see app/Http/Controllers/AiInsightsController.php:262
* @route '/insights/debug'
*/
debugDataSource.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: debugDataSource.url(options),
    method: 'head',
})

const AiInsightsController = { index, askQuestion, getProactiveInsights, generateVisualization, saveInsight, debugDataSource }

export default AiInsightsController