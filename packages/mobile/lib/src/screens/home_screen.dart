import 'package:flutter/material.dart';
import 'package:flutter_platform_widgets/flutter_platform_widgets.dart';
import 'package:graphql_flutter/graphql_flutter.dart';
import 'package:proconvey/src/data/gql.dart';
import 'package:proconvey/src/dto/qr_scanner_result.dart';
import 'package:proconvey/src/screens/qr_scanner_screen.dart';
import 'package:proconvey/src/services/uri/invalid_qr_or_data_exception.dart';
import 'package:proconvey/src/services/uri/uri_parser.dart';
import 'package:proconvey/src/services/yoti_idv_service/yoti_idv_service.dart';
import 'package:proconvey/src/values/assets.dart';
import 'package:proconvey/src/values/colors.dart';
import 'package:uni_links/uni_links.dart';

class HomeScreen extends StatefulWidget {
  static const String routeName = '/';

  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  bool _isLoading = false;
  bool _inSession = false;

  @override
  void initState() {
    super.initState();

    getInitialLink().then((initialLink) {
      _handleIncomingLink(initialLink);
    }).catchError((error) {
      // Do nothing since this was an invalid link
    });

    linkStream.listen((String? link) {
      _handleIncomingLink(link);
    }, onError: (error) {
      // Do nothing since this was an invalid link
    });
  }

  _handleIncomingLink(String? link) {
    if (link != null) {
      Uri parsed;
      try {
        parsed = UriParser.parse(link);
      } on InvalidQrOrDataException {
        // Do nothing since this was an invalid link
        return;
      }
      _triggerSession(
        sessionId: parsed.queryParameters['session_id']!,
        clientToken: parsed.queryParameters['client_token']!,
      );
    }
  }

  _triggerSession({
    required String sessionId,
    required String clientToken,
  }) async {
    setState(() {
      _inSession = true;
    });

    final resultCode = await YotiIdvService.startSession(
      sessionId: sessionId,
      clientToken: clientToken,
    );

    print(resultCode);

    setState(() {
      _inSession = false;
    });

    await graphQlClient.mutate(
      MutationOptions(
        document: gql("""
          mutation idvMobileConnected (\$session_id: ID!, \$reset: Boolean!) {
            idvMobileConnected(session_id: \$session_id, reset: \$reset)
          }
        """),
        variables: {
          'session_id': sessionId,
          'reset': true,
        },
      ),
    );
  }

  _triggerQrScan() async {
    final result = await Navigator.pushNamed(
      context,
      QrScannerScreen.routeName,
    ) as QrScannerResult?;

    if (result == null) {
      return;
    }

    setState(() {
      _isLoading = true;
    });

    final gqlResult = await graphQlClient.mutate(
      MutationOptions(
        document: gql("""
            mutation idvMobileConnected (\$session_id: ID!, \$reset: Boolean!) {
              idvMobileConnected(session_id: \$session_id, reset: \$reset)
            }
          """),
        variables: {
          'session_id': result.sessionId,
          'reset': false,
        },
      ),
    );

    if (gqlResult.hasException) {
      if (!mounted) {
        return;
      }

      print(gqlResult.exception);

      await showPlatformDialog(
        context: context,
        builder: (innerContext) => PlatformAlertDialog(
          title: const Text('Error'),
          content: const Text(
              'There was a problem connecting to ProConvey, please try again'),
          actions: [
            PlatformDialogAction(
              child: const Text('OK'),
              cupertino: (context, platform) => CupertinoDialogActionData(
                isDefaultAction: true,
              ),
              onPressed: () => Navigator.pop(innerContext),
            ),
          ],
        ),
      );

      setState(() {
        _isLoading = false;
      });

      return;
    }

    _triggerSession(
      sessionId: result.sessionId,
      clientToken: result.clientToken,
    );

    setState(() {
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return PlatformScaffold(
      appBar: PlatformAppBar(
        cupertino: (context, platformTarget) => CupertinoNavigationBarData(
          backgroundColor: AppColors.white,
          border: Border.all(color: Colors.transparent),
          brightness: _inSession ? Brightness.dark : Brightness.light,
        ),
      ),
      body: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.only(top: 120),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Flexible(
                    child: FractionallySizedBox(
                      widthFactor: 0.7,
                      child: Image.asset(Assets.logo),
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: Center(
                child: _isLoading
                    ? PlatformCircularProgressIndicator()
                    : Row(
                        children: [
                          Expanded(
                            child: PlatformElevatedButton(
                              onPressed: _triggerQrScan,
                              child: PlatformText('Scan QR Code'),
                            ),
                          ),
                        ],
                      ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
