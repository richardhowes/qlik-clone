import { queryParams, type QueryParams } from './../wayfinder'
/**
* @see routes/web.php:12
* @route '/'
*/
export const home = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ['get','head'],
    url: '/',
}

/**
* @see routes/web.php:12
* @route '/'
*/
home.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return home.definition.url + queryParams(options)
}

/**
* @see routes/web.php:12
* @route '/'
*/
home.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see routes/web.php:12
* @route '/'
*/
home.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see routes/web.php:17
* @route '/dashboard'
*/
export const dashboard = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ['get','head'],
    url: '/dashboard',
}

/**
* @see routes/web.php:17
* @route '/dashboard'
*/
dashboard.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see routes/web.php:17
* @route '/dashboard'
*/
dashboard.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:17
* @route '/dashboard'
*/
dashboard.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AiInsightsController::insights
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
*/
export const insights = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: insights.url(options),
    method: 'get',
})

insights.definition = {
    methods: ['get','head'],
    url: '/insights',
}

/**
* @see \App\Http\Controllers\AiInsightsController::insights
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
*/
insights.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return insights.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AiInsightsController::insights
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
*/
insights.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: insights.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AiInsightsController::insights
* @see app/Http/Controllers/AiInsightsController.php:39
* @route '/insights'
*/
insights.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: insights.url(options),
    method: 'head',
})

/**
* @see routes/web.php:50
* @route '/favorites'
*/
export const favorites = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: favorites.url(options),
    method: 'get',
})

favorites.definition = {
    methods: ['get','head'],
    url: '/favorites',
}

/**
* @see routes/web.php:50
* @route '/favorites'
*/
favorites.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return favorites.definition.url + queryParams(options)
}

/**
* @see routes/web.php:50
* @route '/favorites'
*/
favorites.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: favorites.url(options),
    method: 'get',
})

/**
* @see routes/web.php:50
* @route '/favorites'
*/
favorites.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: favorites.url(options),
    method: 'head',
})

/**
* @see routes/web.php:54
* @route '/collections'
*/
export const collections = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: collections.url(options),
    method: 'get',
})

collections.definition = {
    methods: ['get','head'],
    url: '/collections',
}

/**
* @see routes/web.php:54
* @route '/collections'
*/
collections.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return collections.definition.url + queryParams(options)
}

/**
* @see routes/web.php:54
* @route '/collections'
*/
collections.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: collections.url(options),
    method: 'get',
})

/**
* @see routes/web.php:54
* @route '/collections'
*/
collections.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: collections.url(options),
    method: 'head',
})

/**
* @see routes/web.php:58
* @route '/browse'
*/
export const browse = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: browse.url(options),
    method: 'get',
})

browse.definition = {
    methods: ['get','head'],
    url: '/browse',
}

/**
* @see routes/web.php:58
* @route '/browse'
*/
browse.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return browse.definition.url + queryParams(options)
}

/**
* @see routes/web.php:58
* @route '/browse'
*/
browse.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: browse.url(options),
    method: 'get',
})

/**
* @see routes/web.php:58
* @route '/browse'
*/
browse.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: browse.url(options),
    method: 'head',
})

/**
* @see routes/web.php:62
* @route '/learn'
*/
export const learn = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: learn.url(options),
    method: 'get',
})

learn.definition = {
    methods: ['get','head'],
    url: '/learn',
}

/**
* @see routes/web.php:62
* @route '/learn'
*/
learn.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return learn.definition.url + queryParams(options)
}

/**
* @see routes/web.php:62
* @route '/learn'
*/
learn.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: learn.url(options),
    method: 'get',
})

/**
* @see routes/web.php:62
* @route '/learn'
*/
learn.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: learn.url(options),
    method: 'head',
})

/**
* @see routes/settings.php:21
* @route '/settings/appearance'
*/
export const appearance = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: appearance.url(options),
    method: 'get',
})

appearance.definition = {
    methods: ['get','head'],
    url: '/settings/appearance',
}

/**
* @see routes/settings.php:21
* @route '/settings/appearance'
*/
appearance.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return appearance.definition.url + queryParams(options)
}

/**
* @see routes/settings.php:21
* @route '/settings/appearance'
*/
appearance.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: appearance.url(options),
    method: 'get',
})

/**
* @see routes/settings.php:21
* @route '/settings/appearance'
*/
appearance.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: appearance.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
export const register = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ['get','head'],
    url: '/register',
}

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
register.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
register.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
register.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:19
* @route '/login'
*/
export const login = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ['get','head'],
    url: '/login',
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:19
* @route '/login'
*/
login.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:19
* @route '/login'
*/
login.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:19
* @route '/login'
*/
login.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:42
* @route '/logout'
*/
export const logout = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ['post'],
    url: '/logout',
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:42
* @route '/logout'
*/
logout.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:42
* @route '/logout'
*/
logout.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: logout.url(options),
    method: 'post',
})

