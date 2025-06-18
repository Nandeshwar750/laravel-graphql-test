<!DOCTYPE html>
<html>
<head>
    <title>GraphiQL</title>
    <link rel="stylesheet" href="https://unpkg.com/graphiql@2.4.7/graphiql.min.css" />
    <script src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/graphiql@2.4.7/graphiql.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            height: 100%;
            margin: 0;
            width: 100%;
            overflow: hidden;
        }
        #graphiql {
            height: 100vh;
        }
        .error-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            text-align: center;
            z-index: 1000;
            display: none;
        }
    </style>
</head>
<body>
    <div id="error-container" class="error-container"></div>
    <div id="graphiql"></div>
    <script>
        // Get the current URL without any query parameters
        const baseUrl = window.location.protocol + '//' + window.location.host;
        const graphqlEndpoint = baseUrl + '/graphql';

        // Function to show error message
        function showError(message) {
            const errorContainer = document.getElementById('error-container');
            errorContainer.textContent = message;
            errorContainer.style.display = 'block';
            setTimeout(() => {
                errorContainer.style.display = 'none';
            }, 5000);
        }

        const fetcher = GraphiQL.createFetcher({
            url: graphqlEndpoint,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        // Add error handling
        const fetcherWithErrorHandling = async (...args) => {
            try {
                const result = await fetcher(...args);
                return result;
            } catch (error) {
                console.error('GraphQL request failed:', error);
                showError(`GraphQL request failed: ${error.message}`);
                return {
                    data: null,
                    errors: [{
                        message: 'Failed to fetch: ' + error.message,
                        locations: [],
                        path: [],
                        extensions: {
                            stack: error.stack
                        }
                    }]
                };
            }
        };

        // Test the GraphQL endpoint before rendering
        fetch(graphqlEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                query: '{ __schema { types { name } } }'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(() => {
            // If we get here, the endpoint is working
            ReactDOM.render(
                React.createElement(GraphiQL, {
                    fetcher: fetcherWithErrorHandling,
                    defaultHeaders: JSON.stringify({
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }),
                }),
                document.getElementById('graphiql')
            );
        })
        .catch(error => {
            console.error('GraphQL endpoint test failed:', error);
            showError(`GraphQL endpoint test failed: ${error.message}`);
        });
    </script>
</body>
</html> 