import { queryParams, type QueryParams } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
const index98bffa5fed3ac103efa7d184c2898680 = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index98bffa5fed3ac103efa7d184c2898680.url(options),
    method: 'get',
})

index98bffa5fed3ac103efa7d184c2898680.definition = {
    methods: ['get','head'],
    url: '/queries',
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
index98bffa5fed3ac103efa7d184c2898680.url = (options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    return index98bffa5fed3ac103efa7d184c2898680.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
index98bffa5fed3ac103efa7d184c2898680.get = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index98bffa5fed3ac103efa7d184c2898680.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/queries'
*/
index98bffa5fed3ac103efa7d184c2898680.head = (options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: index98bffa5fed3ac103efa7d184c2898680.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
const index6cac6256b4a7167a12656ea85561efab = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index6cac6256b4a7167a12656ea85561efab.url(args, options),
    method: 'get',
})

index6cac6256b4a7167a12656ea85561efab.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}/queries',
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
index6cac6256b4a7167a12656ea85561efab.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return index6cac6256b4a7167a12656ea85561efab.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
index6cac6256b4a7167a12656ea85561efab.get = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: index6cac6256b4a7167a12656ea85561efab.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::index
* @see app/Http/Controllers/QueryController.php:21
* @route '/data-sources/{data_source}/queries'
*/
index6cac6256b4a7167a12656ea85561efab.head = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: index6cac6256b4a7167a12656ea85561efab.url(args, options),
    method: 'head',
})

export const index = {
    '/queries': index98bffa5fed3ac103efa7d184c2898680,
    '/data-sources/{data_source}/queries': index6cac6256b4a7167a12656ea85561efab,
}

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
const editor1ceb7434d44cbd31c508de76b564a28a = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: editor1ceb7434d44cbd31c508de76b564a28a.url(args, options),
    method: 'get',
})

editor1ceb7434d44cbd31c508de76b564a28a.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}/query',
}

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
editor1ceb7434d44cbd31c508de76b564a28a.url = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
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

    return editor1ceb7434d44cbd31c508de76b564a28a.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
editor1ceb7434d44cbd31c508de76b564a28a.get = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: editor1ceb7434d44cbd31c508de76b564a28a.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query'
*/
editor1ceb7434d44cbd31c508de76b564a28a.head = (args: { data_source: string | number } | [data_source: string | number ] | string | number, options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: editor1ceb7434d44cbd31c508de76b564a28a.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
const editor41c7c293ca265030dc2ade0ddba0ff52 = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: editor41c7c293ca265030dc2ade0ddba0ff52.url(args, options),
    method: 'get',
})

editor41c7c293ca265030dc2ade0ddba0ff52.definition = {
    methods: ['get','head'],
    url: '/data-sources/{data_source}/query/{query}',
}

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
editor41c7c293ca265030dc2ade0ddba0ff52.url = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }) => {
    if (Array.isArray(args)) {
        args = {
            data_source: args[0],
            query: args[1],
        }
    }

    const parsedArgs = {
        data_source: args.data_source,
        query: typeof args.query === 'object'
        ? args.query.id
        : args.query,
    }

    return editor41c7c293ca265030dc2ade0ddba0ff52.definition.url
            .replace('{data_source}', parsedArgs.data_source.toString())
            .replace('{query}', parsedArgs.query.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
editor41c7c293ca265030dc2ade0ddba0ff52.get = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'get',
} => ({
    url: editor41c7c293ca265030dc2ade0ddba0ff52.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QueryController::editor
* @see app/Http/Controllers/QueryController.php:48
* @route '/data-sources/{data_source}/query/{query}'
*/
editor41c7c293ca265030dc2ade0ddba0ff52.head = (args: { data_source: string | number, query: number | { id: number } } | [data_source: string | number, query: number | { id: number } ], options?: { query?: QueryParams, mergeQuery?: QueryParams }): {
    url: string,
    method: 'head',
} => ({
    url: editor41c7c293ca265030dc2ade0ddba0ff52.url(args, options),
    method: 'head',
})

export const editor = {
    '/data-sources/{data_source}/query': editor1ceb7434d44cbd31c508de76b564a28a,
    '/data-sources/{data_source}/query/{query}': editor41c7c293ca265030dc2ade0ddba0ff52,
}

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

const QueryController = { index, editor, execute, save, destroy }

export default QueryController