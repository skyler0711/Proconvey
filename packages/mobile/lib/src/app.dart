import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_platform_widgets/flutter_platform_widgets.dart';
import 'package:graphql_flutter/graphql_flutter.dart';
import 'package:proconvey/src/data/gql.dart';
import 'package:proconvey/src/screens/home_screen.dart';
import 'package:proconvey/src/screens/qr_scanner_screen.dart';
import 'package:proconvey/src/values/styles.dart';

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return Theme(
      data: Styles.materialThemeData,
      child: PlatformProvider(
        builder: (context) => GraphQLProvider(
          client: flutterGraphQlClient,
          child: PlatformApp(
            localizationsDelegates: const [
              DefaultMaterialLocalizations.delegate,
              DefaultWidgetsLocalizations.delegate,
              DefaultCupertinoLocalizations.delegate,
            ],
            material: (_, __) => MaterialAppData(
              theme: Styles.materialThemeData,
            ),
            cupertino: (_, __) => CupertinoAppData(
              theme: Styles.cupertinoThemeData,
            ),
            initialRoute: '/',
            routes: {
              HomeScreen.routeName: (context) => const HomeScreen(),
              QrScannerScreen.routeName: (context) => const QrScannerScreen(),
            },
          ),
        ),
      ),
    );
  }
}
