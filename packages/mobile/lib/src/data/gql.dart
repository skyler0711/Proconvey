import 'package:flutter/material.dart';
import 'package:graphql_flutter/graphql_flutter.dart';
import 'package:proconvey/src/values/config.dart';

final HttpLink httpLink = HttpLink(
  Config.graphQlEndpoint,
  defaultHeaders: {
    'Accept': 'application/json',
  },
);

final GraphQLClient graphQlClient = GraphQLClient(
  link: httpLink,
  cache: GraphQLCache(
    store: InMemoryStore(),
  ),
);

ValueNotifier<GraphQLClient> flutterGraphQlClient =
    ValueNotifier(graphQlClient);
