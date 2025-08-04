import { queryParams, type QueryParams } from './../../wayfinder'
import editor from './editor'
/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
export const editor = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: editor.url(args, options),
    method: 'get',
})

editor.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}/query',
}

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
editor.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return editor.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
editor.get = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: editor.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
editor.head = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: editor.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\QueryController::execute
* @see app/Http/Controllers/QueryController.php:72
* @route '/data-sources/{data_source}/query/execute'
*/
export const execute = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: execute.url(args, options),
    method: 'post',
})

execute.definition = {
    methods: ['post'],
    url: '/data-sources/{data_source}/query/execute',
}

/**
* @see \App\Http\Controllers\QueryController::execute
* @see app/Http/Controllers/QueryController.php:72
* @route '/data-sources/{data_source}/query/execute'
*/
execute.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return execute.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::execute
* @see app/Http/Controllers/QueryController.php:72
* @route '/data-sources/{data_source}/query/execute'
*/
execute.post = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: execute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\QueryController::save
* @see app/Http/Controllers/QueryController.php:102
* @route '/data-sources/{data_source}/query/save'
*/
export const save = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: save.url(args, options),
    method: 'post',
})

save.definition = {
    methods: ['post'],
    url: '/data-sources/{data_source}/query/save',
}

/**
* @see \App\Http\Controllers\QueryController::save
* @see app/Http/Controllers/QueryController.php:102
* @route '/data-sources/{data_source}/query/save'
*/
save.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return save.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::save
* @see app/Http/Controllers/QueryController.php:102
* @route '/data-sources/{data_source}/query/save'
*/
save.post = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'post',
} => ({
    url: save.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\QueryController::destroy
* @see app/Http/Controllers/QueryController.php:124
* @route '/queries/{query}'
*/
export const destroy = (args: { query: number | { id: number } } | [query: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ['delete'],
    url: '/queries/{query}',
}

/**
* @see \App\Http\Controllers\QueryController::destroy
* @see app/Http/Controllers/QueryController.php:124
* @route '/queries/{query}'
*/
destroy.url = (args: { query: number | { id: number } } | [query: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { query: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { query: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            query: args[0],
        }
    }

    const parsedArgs = {
        query: typeof args.query === 'object'
        ? args.query.id
        : args.query,
    }

    return destroy.definition.url
            .replace('{query}', parsedArgs.query.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::destroy
* @see app/Http/Controllers/QueryController.php:124
* @route '/queries/{query}'
*/
destroy.delete = (args: { query: number | { id: number } } | [query: number | { id: number } ] | number | { id: number }, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'delete',
} => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const query = {
    editor,
    execute,
    save,
    destroy,
}

export default query