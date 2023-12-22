const tailwindConfig = require('@proconvey/ui/tailwind.config.js')

module.exports = {
  ...tailwindConfig,
  content: [
    './**/*.{js,jsx,ts,tsx}',
    '../ui/**/*.{js,ts,jsx,tsx}',
  ],
}
