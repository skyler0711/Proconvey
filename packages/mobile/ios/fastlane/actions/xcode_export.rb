module Fastlane
  module Actions
    module SharedValues
      XCODE_EXPORT_CUSTOM_VALUE = :XCODE_EXPORT_CUSTOM_VALUE
    end

    class XcodeExportAction < Action
      def self.run(params)
        
        sh("xcodebuild -quiet -exportArchive -archivePath ../build/ios/archive/Runner.xcarchive -exportOptionsPlist exportOptions.plist -exportPath build")

      end
    end
  end
end
