module Fastlane
  module Actions
    module SharedValues
      FIND_API_CUSTOM_VALUE = :FIND_API_CUSTOM_VALUE
    end

    class FindIpaAction < Action
      def self.run(params)

        (sh 'find ${PWD}/build -type f -name "*.ipa" -maxdepth 1').strip

      end
    end
  end
end
