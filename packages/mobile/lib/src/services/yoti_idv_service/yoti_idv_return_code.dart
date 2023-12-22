enum YotiIdvReturnCode {
  success(0),
  noError(1000),
  unauthorisedRequest(2000),
  sessionNotFound(2001),
  sdkLaunchedWithoutSessionToken(2002),
  sdkLaunchedWithoutSessionId(2004),
  yotiServicesUnavailable(3000),
  networkRequestError(3001),
  noNetwork(3002),
  noCameraPermissions(4000),
  wrongSubmission(4001),
  noCamera(5000),
  noMoreTriesForLiveness(5002),
  sdkOutOfDate(5003),
  internalError(5004),
  documentScanningError(5005),
  livenessError(5006),
  unsupportedConfiguration(5008),
  storageError(5009),
  documentCaptureDependencyNotFound(6000),
  livenessZoomDependencyNotFound(6001),
  supplementaryDocumentDependencyNotFound(6002),
  faceCaptureDependencyNotFound(6003),
  noRequiredDocuments(7000);

  const YotiIdvReturnCode(this.value);
  final int value;

  static YotiIdvReturnCode? fromValue(int value) {
    return YotiIdvReturnCode.values.firstWhere(
      (element) => element.value == value,
    );
  }
}
