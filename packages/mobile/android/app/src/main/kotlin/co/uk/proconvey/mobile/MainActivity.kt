package uk.co.proconvey.mobile

import android.content.Intent
import androidx.annotation.NonNull
import com.yoti.mobile.android.yotisdkcore.YotiSdk
import com.yoti.mobile.android.yotisdkcore.YOTI_SDK_REQUEST_CODE
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel

class MainActivity: FlutterActivity() {

  private lateinit var yotiSdk: YotiSdk
  private lateinit var resultFun: MethodChannel.Result

  override fun configureFlutterEngine(@NonNull flutterEngine: FlutterEngine) {
    super.configureFlutterEngine(flutterEngine)

      this.yotiSdk = YotiSdk(this)

    MethodChannel(flutterEngine.dartExecutor.binaryMessenger, "uk.co.proconvey.mobile/yoti_idv")
      .setMethodCallHandler { call, result ->
        resultFun = result
        if (call.method == "startSession") {
          this.yotiSdk
            .setSessionId(call.argument<String>("sessionId")!!)
            .setClientSessionToken(call.argument<String>("clientToken")!!)
            .start(this)
        } else {
          result.notImplemented()
        }
      }
  }

  override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == YOTI_SDK_REQUEST_CODE) {
            this.resultFun.success(yotiSdk.sessionStatusCode)
        }
    }
}
