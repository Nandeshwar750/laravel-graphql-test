{{-- See https://github.com/graphql/graphiql/blob/main/examples/graphiql-cdn/index.html. --}}
@php
use MLL\GraphiQL\GraphiQLAsset;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GraphiQL</title>
    <style>
        body {
            margin: 0;
            overflow: hidden; /* in Firefox */
        }

        #graphiql {
            height: 100dvh;
        }

        #graphiql-loading {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }

        .docExplorerWrap {
            /* Allow scrolling, see https://github.com/graphql/graphiql/issues/3098. */
            overflow: auto !important;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/graphiql@2.4.7/graphiql.min.css" />
    <script src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/graphiql@2.4.7/graphiql.min.js"></script>
    <link rel="shortcut icon" href="{{ GraphiQLAsset::favicon() }}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body style="margin: 0; overflow-x: hidden; overflow-y: hidden">
    <div id="graphiql" style="height: 100vh;"></div>
    <script>
        const fetcher = GraphiQL.createFetcher({
            url: '{{ config('app.url') }}/graphql',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        ReactDOM.render(
            React.createElement(GraphiQL, {
                fetcher: fetcher,
                defaultHeaders: JSON.stringify({
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }),
            }),
            document.getElementById('graphiql')
        );
    </script>
</body>
</html>
