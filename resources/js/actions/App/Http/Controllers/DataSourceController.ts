import { queryParams, type QueryParams } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DataSourceController::index
* @see app/Http/Controllers/DataSourceController.php:21
* @route '/data-sources'
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
    url: '/data-sources',
}

/**
* @see \App\Http\Controllers\DataSourceController::index
* @see app/Http/Controllers/DataSourceController.php:21
* @route '/data-sources'
*/
index.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::index
* @see app/Http/Controllers/DataSourceController.php:21
* @route '/data-sources'
*/
index.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataSourceController::index
* @see app/Http/Controllers/DataSourceController.php:21
* @route '/data-sources'
*/
index.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DataSourceController::create
* @see app/Http/Controllers/DataSourceController.php:32
* @route '/data-sources/create'
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
    url: '/data-sources/create',
}

/**
* @see \App\Http\Controllers\DataSourceController::create
* @see app/Http/Controllers/DataSourceController.php:32
* @route '/data-sources/create'
*/
create.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::create
* @see app/Http/Controllers/DataSourceController.php:32
* @route '/data-sources/create'
*/
create.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataSourceController::create
* @see app/Http/Controllers/DataSourceController.php:32
* @route '/data-sources/create'
*/
create.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DataSourceController::store
* @see app/Http/Controllers/DataSourceController.php:45
* @route '/data-sources'
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
    url: '/data-sources',
}

/**
* @see \App\Http\Controllers\DataSourceController::store
* @see app/Http/Controllers/DataSourceController.php:45
* @route '/data-sources'
*/
store.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::store
* @see app/Http/Controllers/DataSourceController.php:45
* @route '/data-sources'
*/
store.post = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DataSourceController::show
* @see app/Http/Controllers/DataSourceController.php:81
* @route '/data-sources/{data_source}'
*/
export const show = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}',
}

/**
* @see \App\Http\Controllers\DataSourceController::show
* @see app/Http/Controllers/DataSourceController.php:81
* @route '/data-sources/{data_source}'
*/
show.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return show.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::show
* @see app/Http/Controllers/DataSourceController.php:81
* @route '/data-sources/{data_source}'
*/
show.get = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataSourceController::show
* @see app/Http/Controllers/DataSourceController.php:81
* @route '/data-sources/{data_source}'
*/
show.head = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DataSourceController::edit
* @see app/Http/Controllers/DataSourceController.php:94
* @route '/data-sources/{data_source}/edit'
*/
export const edit = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}/edit',
}

/**
* @see \App\Http\Controllers\DataSourceController::edit
* @see app/Http/Controllers/DataSourceController.php:94
* @route '/data-sources/{data_source}/edit'
*/
edit.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return edit.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::edit
* @see app/Http/Controllers/DataSourceController.php:94
* @route '/data-sources/{data_source}/edit'
*/
edit.get = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataSourceController::edit
* @see app/Http/Controllers/DataSourceController.php:94
* @route '/data-sources/{data_source}/edit'
*/
edit.head = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DataSourceController::update
* @see app/Http/Controllers/DataSourceController.php:116
* @route '/data-sources/{data_source}'
*/
export const update = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ['put','patch'],
    url: '/data-sources/{data_source}',
}

/**
* @see \App\Http\Controllers\DataSourceController::update
* @see app/Http/Controllers/DataSourceController.php:116
* @route '/data-sources/{data_source}'
*/
update.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return update.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::update
* @see app/Http/Controllers/DataSourceController.php:116
* @route '/data-sources/{data_source}'
*/
update.put = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'put',
} => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\DataSourceController::update
* @see app/Http/Controllers/DataSourceController.php:116
* @route '/data-sources/{data_source}'
*/
update.patch = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'patch',
} => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\DataSourceController::destroy
* @see app/Http/Controllers/DataSourceController.php:146
* @route '/data-sources/{data_source}'
*/
export const destroy = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ['delete'],
    url: '/data-sources/{data_source}',
}

/**
* @see \App\Http\Controllers\DataSourceController::destroy
* @see app/Http/Controllers/DataSourceController.php:146
* @route '/data-sources/{data_source}'
*/
destroy.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return destroy.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::destroy
* @see app/Http/Controllers/DataSourceController.php:146
* @route '/data-sources/{data_source}'
*/
destroy.delete = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DataSourceController::test
* @see app/Http/Controllers/DataSourceController.php:156
* @route '/data-sources/{data_source}/test'
*/
export const test = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: test.url(args, options),
    method: 'post',
})

test.definition = {
    methods: ['post'],
    url: '/data-sources/{data_source}/test',
}

/**
* @see \App\Http\Controllers\DataSourceController::test
* @see app/Http/Controllers/DataSourceController.php:156
* @route '/data-sources/{data_source}/test'
*/
test.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return test.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::test
* @see app/Http/Controllers/DataSourceController.php:156
* @route '/data-sources/{data_source}/test'
*/
test.post = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: test.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DataSourceController::getConfigFields
* @see app/Http/Controllers/DataSourceController.php:168
* @route '/data-source-config-fields'
*/
export const getConfigFields = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: getConfigFields.url(options),
    method: 'get',
})

getConfigFields.definition = {
    methods: ['get','head'],
    url: '/data-source-config-fields',
}

/**
* @see \App\Http\Controllers\DataSourceController::getConfigFields
* @see app/Http/Controllers/DataSourceController.php:168
* @route '/data-source-config-fields'
*/
getConfigFields.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return getConfigFields.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DataSourceController::getConfigFields
* @see app/Http/Controllers/DataSourceController.php:168
* @route '/data-source-config-fields'
*/
getConfigFields.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: getConfigFields.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DataSourceController::getConfigFields
* @see app/Http/Controllers/DataSourceController.php:168
* @route '/data-source-config-fields'
*/
getConfigFields.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: getConfigFields.url(options),
    method: 'head',
})

const DataSourceController = { index, create, store, show, edit, update, destroy, test, getConfigFields }

export default DataSourceController