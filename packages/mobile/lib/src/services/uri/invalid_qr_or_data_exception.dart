class InvalidQrOrDataException implements Exception {
  final String message;

  InvalidQrOrDataException(this.message);

  @override
  String toString() => message;
}
