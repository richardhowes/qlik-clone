import { queryParams, type QueryParams } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\DashboardController::store
* @see app/Http/Controllers/DashboardController.php:116
* @route '/dashboards/{dashboard}/widgets'
*/
export const store = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ['post'],
    url: '/dashboards/{dashboard}/widgets',
}

/**
* @see \App\Http\Controllers\DashboardController::store
* @see app/Http/Controllers/DashboardController.php:116
* @route '/dashboards/{dashboard}/widgets'
*/
store.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { dashboard: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { dashboard: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            dashboard: args[0],
        }
    }

    const parsedArgs = {
        dashboard: typeof args.dashboard === 'object'
        ? args.dashboard.id
        : args.dashboard,
    }

    return store.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::store
* @see app/Http/Controllers/DashboardController.php:116
* @route '/dashboards/{dashboard}/widgets'
*/
store.post = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DashboardController::update
* @see app/Http/Controllers/DashboardController.php:138
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
export const update = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ['put'],
    url: '/dashboards/{dashboard}/widgets/{widget}',
}

/**
* @see \App\Http\Controllers\DashboardController::update
* @see app/Http/Controllers/DashboardController.php:138
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
update.url = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (Array.isArray(args)) {
        args = {
            dashboard: args[0],
            widget: args[1],
        }
    }

    const parsedArgs = {
        dashboard: typeof args.dashboard === 'object'
        ? args.dashboard.id
        : args.dashboard,
        widget: typeof args.widget === 'object'
        ? args.widget.id
        : args.widget,
    }

    return update.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace('{widget}', parsedArgs.widget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::update
* @see app/Http/Controllers/DashboardController.php:138
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
update.put = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\DashboardController::destroy
* @see app/Http/Controllers/DashboardController.php:159
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
export const destroy = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ['delete'],
    url: '/dashboards/{dashboard}/widgets/{widget}',
}

/**
* @see \App\Http\Controllers\DashboardController::destroy
* @see app/Http/Controllers/DashboardController.php:159
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
destroy.url = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (Array.isArray(args)) {
        args = {
            dashboard: args[0],
            widget: args[1],
        }
    }

    const parsedArgs = {
        dashboard: typeof args.dashboard === 'object'
        ? args.dashboard.id
        : args.dashboard,
        widget: typeof args.widget === 'object'
        ? args.widget.id
        : args.widget,
    }

    return destroy.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace('{widget}', parsedArgs.widget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::destroy
* @see app/Http/Controllers/DashboardController.php:159
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
destroy.delete = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DashboardController::reorder
* @see app/Http/Controllers/DashboardController.php:172
* @route '/dashboards/{dashboard}/widgets/reorder'
*/
export const reorder = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: reorder.url(args, options),
    method: 'put',
})

reorder.definition = {
    methods: ['put'],
    url: '/dashboards/{dashboard}/widgets/reorder',
}

/**
* @see \App\Http\Controllers\DashboardController::reorder
* @see app/Http/Controllers/DashboardController.php:172
* @route '/dashboards/{dashboard}/widgets/reorder'
*/
reorder.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { dashboard: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { dashboard: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            dashboard: args[0],
        }
    }

    const parsedArgs = {
        dashboard: typeof args.dashboard === 'object'
        ? args.dashboard.id
        : args.dashboard,
    }

    return reorder.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::reorder
* @see app/Http/Controllers/DashboardController.php:172
* @route '/dashboards/{dashboard}/widgets/reorder'
*/
reorder.put = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: reorder.url(args, options),
    method: 'put',
})

const widgets = {
    store,
    update,
    destroy,
    reorder,
}

export default widgets