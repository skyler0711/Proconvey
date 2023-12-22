import UIKit
import Flutter
import YotiSDKCommon
import YotiSDKCore
import YotiSDKDocument
import YotiSDKFaceTec
import YotiSDKFaceCapture

@UIApplicationMain
@objc class AppDelegate: FlutterAppDelegate {
    
    var navigationController: UINavigationController!
    
    var yotiResultFunc: FlutterResult!
    var yotiSessionId: String!
    var yotiClientToken: String!
    
    override func application(
        _ application: UIApplication,
        didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?
    ) -> Bool {
        
        let controller: FlutterViewController = window?.rootViewController as! FlutterViewController
        let yotiChannel = FlutterMethodChannel(name: "uk.co.proconvey.mobile/yoti_idv", binaryMessenger: controller.binaryMessenger)
        
        yotiChannel.setMethodCallHandler({
            [weak self] (call: FlutterMethodCall, result: @escaping FlutterResult) -> Void in
            guard call.method == "startSession" else {
                result(FlutterMethodNotImplemented)
                return
            }
            
            let args = call.arguments as? Dictionary<String, String>
            
            self?.yotiResultFunc = result
            self?.yotiSessionId = args?["sessionId"]
            self?.yotiClientToken = args?["clientToken"]
            self?.startYotiSession(result: result)
        })
        
        GeneratedPluginRegistrant.register(with: self)
        
        self.navigationController = UINavigationController(rootViewController: controller)
        self.window.rootViewController = self.navigationController
        self.navigationController.setNavigationBarHidden(true, animated: false)
        self.window.makeKeyAndVisible()
        
        return super.application(application, didFinishLaunchingWithOptions: launchOptions)
    }
}

extension AppDelegate: YotiSDKDataSource {
    private func startYotiSession(result: FlutterResult) {
        let navigationController = YotiSDKNavigationController()
        navigationController.sdkDataSource = self
        navigationController.sdkDelegate = self
        self.navigationController.present(navigationController, animated: true)
    }
    
    func sessionID(for navigationController: YotiSDKNavigationController) -> String {
        self.yotiSessionId
    }
    
    func sessionToken(for navigationController: YotiSDKNavigationController) -> String {
        self.yotiClientToken
    }
    
    func supportedModuleTypes(for navigationController: YotiSDKNavigationController) -> [YotiSDKModule.Type] {
        [
            YotiSDKDocumentModule.self,
            YotiSDKFaceTecModule.self,
            YotiSDKFaceCaptureModule.self
        ]
    }
}

extension AppDelegate: YotiSDKDelegate {
    func primaryColor(for navigationController: YotiSDKNavigationController) -> UIColor {
        return UIColor(red: 103, green: 65, blue: 134)
    }
    
    func navigationController(_ navigationController: YotiSDKNavigationController, didFinishWithResult result: YotiSDKResult) {
        self.navigationController.dismiss(animated: true)
        
        switch result {
        case .success:
            self.yotiResultFunc(nil)
            break
        case .failure(let error):
            self.yotiResultFunc(error.errorCode)
            print(error)
        }
    }
}
