import 'dart:async';
import 'package:flutter/services.dart';
import 'package:proconvey/src/services/yoti_idv_service/yoti_idv_return_code.dart';

class YotiIdvService {
  static const _platform = MethodChannel('uk.co.proconvey.mobile/yoti_idv');

  static Future<YotiIdvReturnCode?> startSession({
    required String sessionId,
    required String clientToken,
  }) async {
    final int? result = await _platform.invokeMethod('startSession', {
      'sessionId': sessionId,
      'clientToken': clientToken,
    });

    if (result == null) {
      return null;
    }

    final code = YotiIdvReturnCode.fromValue(result);
    return code;
  }
}
