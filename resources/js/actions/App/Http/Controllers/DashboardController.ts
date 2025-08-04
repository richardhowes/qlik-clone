import { queryParams, type QueryParams } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:14
* @route '/dashboards'
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
    url: '/dashboards',
}

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:14
* @route '/dashboards'
*/
index.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:14
* @route '/dashboards'
*/
index.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:14
* @route '/dashboards'
*/
index.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::create
* @see app/Http/Controllers/DashboardController.php:25
* @route '/dashboards/create'
*/
export const create = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ['get','head'],
    url: '/dashboards/create',
}

/**
* @see \App\Http\Controllers\DashboardController::create
* @see app/Http/Controllers/DashboardController.php:25
* @route '/dashboards/create'
*/
create.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::create
* @see app/Http/Controllers/DashboardController.php:25
* @route '/dashboards/create'
*/
create.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::create
* @see app/Http/Controllers/DashboardController.php:25
* @route '/dashboards/create'
*/
create.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::store
* @see app/Http/Controllers/DashboardController.php:30
* @route '/dashboards'
*/
export const store = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ['post'],
    url: '/dashboards',
}

/**
* @see \App\Http\Controllers\DashboardController::store
* @see app/Http/Controllers/DashboardController.php:30
* @route '/dashboards'
*/
store.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::store
* @see app/Http/Controllers/DashboardController.php:30
* @route '/dashboards'
*/
store.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DashboardController::show
* @see app/Http/Controllers/DashboardController.php:46
* @route '/dashboards/{dashboard}'
*/
export const show = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ['get','head'],
    url: '/dashboards/{dashboard}',
}

/**
* @see \App\Http\Controllers\DashboardController::show
* @see app/Http/Controllers/DashboardController.php:46
* @route '/dashboards/{dashboard}'
*/
show.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return show.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::show
* @see app/Http/Controllers/DashboardController.php:46
* @route '/dashboards/{dashboard}'
*/
show.get = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::show
* @see app/Http/Controllers/DashboardController.php:46
* @route '/dashboards/{dashboard}'
*/
show.head = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::edit
* @see app/Http/Controllers/DashboardController.php:57
* @route '/dashboards/{dashboard}/edit'
*/
export const edit = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ['get','head'],
    url: '/dashboards/{dashboard}/edit',
}

/**
* @see \App\Http\Controllers\DashboardController::edit
* @see app/Http/Controllers/DashboardController.php:57
* @route '/dashboards/{dashboard}/edit'
*/
edit.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return edit.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::edit
* @see app/Http/Controllers/DashboardController.php:57
* @route '/dashboards/{dashboard}/edit'
*/
edit.get = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::edit
* @see app/Http/Controllers/DashboardController.php:57
* @route '/dashboards/{dashboard}/edit'
*/
edit.head = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::update
* @see app/Http/Controllers/DashboardController.php:74
* @route '/dashboards/{dashboard}'
*/
export const update = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ['put','patch'],
    url: '/dashboards/{dashboard}',
}

/**
* @see \App\Http\Controllers\DashboardController::update
* @see app/Http/Controllers/DashboardController.php:74
* @route '/dashboards/{dashboard}'
*/
update.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return update.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::update
* @see app/Http/Controllers/DashboardController.php:74
* @route '/dashboards/{dashboard}'
*/
update.put = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\DashboardController::update
* @see app/Http/Controllers/DashboardController.php:74
* @route '/dashboards/{dashboard}'
*/
update.patch = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'patch',
} => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\DashboardController::destroy
* @see app/Http/Controllers/DashboardController.php:96
* @route '/dashboards/{dashboard}'
*/
export const destroy = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ['delete'],
    url: '/dashboards/{dashboard}',
}

/**
* @see \App\Http\Controllers\DashboardController::destroy
* @see app/Http/Controllers/DashboardController.php:96
* @route '/dashboards/{dashboard}'
*/
destroy.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return destroy.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::destroy
* @see app/Http/Controllers/DashboardController.php:96
* @route '/dashboards/{dashboard}'
*/
destroy.delete = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DashboardController::toggleFavorite
* @see app/Http/Controllers/DashboardController.php:105
* @route '/dashboards/{dashboard}/favorite'
*/
export const toggleFavorite = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: toggleFavorite.url(args, options),
    method: 'post',
})

toggleFavorite.definition = {
    methods: ['post'],
    url: '/dashboards/{dashboard}/favorite',
}

/**
* @see \App\Http\Controllers\DashboardController::toggleFavorite
* @see app/Http/Controllers/DashboardController.php:105
* @route '/dashboards/{dashboard}/favorite'
*/
toggleFavorite.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return toggleFavorite.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::toggleFavorite
* @see app/Http/Controllers/DashboardController.php:105
* @route '/dashboards/{dashboard}/favorite'
*/
toggleFavorite.post = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: toggleFavorite.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DashboardController::addWidget
* @see app/Http/Controllers/DashboardController.php:116
* @route '/dashboards/{dashboard}/widgets'
*/
export const addWidget = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: addWidget.url(args, options),
    method: 'post',
})

addWidget.definition = {
    methods: ['post'],
    url: '/dashboards/{dashboard}/widgets',
}

/**
* @see \App\Http\Controllers\DashboardController::addWidget
* @see app/Http/Controllers/DashboardController.php:116
* @route '/dashboards/{dashboard}/widgets'
*/
addWidget.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return addWidget.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::addWidget
* @see app/Http/Controllers/DashboardController.php:116
* @route '/dashboards/{dashboard}/widgets'
*/
addWidget.post = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: addWidget.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DashboardController::updateWidget
* @see app/Http/Controllers/DashboardController.php:138
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
export const updateWidget = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: updateWidget.url(args, options),
    method: 'put',
})

updateWidget.definition = {
    methods: ['put'],
    url: '/dashboards/{dashboard}/widgets/{widget}',
}

/**
* @see \App\Http\Controllers\DashboardController::updateWidget
* @see app/Http/Controllers/DashboardController.php:138
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
updateWidget.url = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return updateWidget.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace('{widget}', parsedArgs.widget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::updateWidget
* @see app/Http/Controllers/DashboardController.php:138
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
updateWidget.put = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: updateWidget.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\DashboardController::deleteWidget
* @see app/Http/Controllers/DashboardController.php:159
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
export const deleteWidget = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: deleteWidget.url(args, options),
    method: 'delete',
})

deleteWidget.definition = {
    methods: ['delete'],
    url: '/dashboards/{dashboard}/widgets/{widget}',
}

/**
* @see \App\Http\Controllers\DashboardController::deleteWidget
* @see app/Http/Controllers/DashboardController.php:159
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
deleteWidget.url = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return deleteWidget.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace('{widget}', parsedArgs.widget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::deleteWidget
* @see app/Http/Controllers/DashboardController.php:159
* @route '/dashboards/{dashboard}/widgets/{widget}'
*/
deleteWidget.delete = (args: { dashboard: number | { id: number }, widget: number | { id: number } } | [dashboard: number | { id: number }, widget: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: deleteWidget.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DashboardController::reorderWidgets
* @see app/Http/Controllers/DashboardController.php:172
* @route '/dashboards/{dashboard}/widgets/reorder'
*/
export const reorderWidgets = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: reorderWidgets.url(args, options),
    method: 'put',
})

reorderWidgets.definition = {
    methods: ['put'],
    url: '/dashboards/{dashboard}/widgets/reorder',
}

/**
* @see \App\Http\Controllers\DashboardController::reorderWidgets
* @see app/Http/Controllers/DashboardController.php:172
* @route '/dashboards/{dashboard}/widgets/reorder'
*/
reorderWidgets.url = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return reorderWidgets.definition.url
            .replace('{dashboard}', parsedArgs.dashboard.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::reorderWidgets
* @see app/Http/Controllers/DashboardController.php:172
* @route '/dashboards/{dashboard}/widgets/reorder'
*/
reorderWidgets.put = (args: { dashboard: number | { id: number } } | [dashboard: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: reorderWidgets.url(args, options),
    method: 'put',
})

const DashboardController = { index, create, store, show, edit, update, destroy, toggleFavorite, addWidget, updateWidget, deleteWidget, reorderWidgets }

export default DashboardController