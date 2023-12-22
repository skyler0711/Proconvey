import 'dart:ui';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_platform_widgets/flutter_platform_widgets.dart';
import 'package:proconvey/src/dto/qr_scanner_result.dart';
import 'package:proconvey/src/services/uri/invalid_qr_or_data_exception.dart';
import 'package:proconvey/src/services/uri/uri_parser.dart';
import 'package:proconvey/src/values/colors.dart';
import 'package:mobile_scanner/mobile_scanner.dart';

class QrScannerScreen extends StatefulWidget {
  static const String routeName = '/qr-scanner';

  const QrScannerScreen({super.key});

  @override
  State<QrScannerScreen> createState() => _QrScannerScreenState();
}

class _QrScannerScreenState extends State<QrScannerScreen> {
  late final MobileScannerController _controller;
  bool _detected = false;

  @override
  void initState() {
    super.initState();
    _controller = MobileScannerController(
      detectionSpeed: DetectionSpeed.noDuplicates,
      formats: [BarcodeFormat.qrCode],
    );
  }

  _onInvalidQrOrData() {
    showPlatformDialog(
      context: context,
      builder: (innerContext) => PlatformAlertDialog(
        title: const Text('Invalid QR code'),
        content: const Text('The QR code you scanned is invalid.'),
        actions: [
          PlatformDialogAction(
            child: const Text('OK'),
            onPressed: () {
              Navigator.pop(innerContext);
              _detected = false;
            },
          ),
        ],
      ),
    );
  }

  _onDetect(BarcodeCapture capture) {
    // Expects URI in this format:
    // https://api.proconvey.co.uk/webhooks/mobile?action=idv&session_id=405969b7-f036-48fa-971b-72cb79daeadd&client_token=0a22217e-f7f5-435d-82f1-94141eea20be
    if (_detected) {
      return;
    }
    _detected = true;

    final value = capture.barcodes.first.rawValue ?? '';
    Uri uri;

    try {
      uri = UriParser.parse(value);
    } on InvalidQrOrDataException {
      _onInvalidQrOrData();
      return;
    }

    Navigator.pop(
      context,
      QrScannerResult(
        sessionId: uri.queryParameters['session_id']!,
        clientToken: uri.queryParameters['client_token']!,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return PlatformScaffold(
      material: (context, platform) => MaterialScaffoldData(
        extendBodyBehindAppBar: true,
      ),
      appBar: PlatformAppBar(
        backgroundColor: AppColors.transparent,
        title: const Text('Scan the QR code'),
        cupertino: (context, platform) => CupertinoNavigationBarData(
          leading: CupertinoNavigationBarBackButton(
            onPressed: () {
              Navigator.pop(context);
            },
            color: AppColors.white,
          ),
        ),
        material: (context, platform) => MaterialAppBarData(
          backgroundColor: AppColors.black.withOpacity(0.2),
          systemOverlayStyle: SystemUiOverlayStyle.light,
          leading: const BackButton(
            color: AppColors.white,
          ),
        ),
      ),
      body: Stack(
        children: [
          MobileScanner(
            onDetect: _onDetect,
            controller: _controller,
          ),
        ],
      ),
    );
  }
}
