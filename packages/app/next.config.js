const { withSentryConfig, SentryWebpackPluginOptions } = require('@sentry/nextjs')

const path = require('path')

/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: false,
  swcMinify: true,

  images: {
    unoptimized: true,
  },

  sentry: {
    disableServerWebpackPlugin: true,
    disableClientWebpackPlugin: true,
    autoInstrumentServerFunctions: false,
  },

  transpilePackages: ['@proconvey/ui'],
}

/** @type SentryWebpackPluginOptions */
const sentryWebpackPluginOptions = {

}

module.exports = withSentryConfig(nextConfig, sentryWebpackPluginOptions)
