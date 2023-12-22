import 'package:proconvey/src/services/uri/invalid_qr_or_data_exception.dart';

class UriParser {
  static Uri parse(String value) {
    final uri = Uri.tryParse(value);

    if (uri == null) {
      throw InvalidQrOrDataException('URI is null');
    }

    if (!uri.queryParameters.containsKey('action') ||
        uri.queryParameters['action'] != 'idv') {
      throw InvalidQrOrDataException('Invalid action');
    }

    if (!uri.queryParameters.containsKey('session_id') ||
        !uri.queryParameters.containsKey('client_token') ||
        uri.queryParameters['session_id']!.isEmpty ||
        uri.queryParameters['client_token']!.isEmpty) {
      throw InvalidQrOrDataException('Invalid session_id or client_token');
    }

    return uri;
  }
}
