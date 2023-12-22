import '../styles/globals.css'
import 'react-loading-skeleton/dist/skeleton.css'
import type { AppProps } from 'next/app'
import { withUrqlClient } from 'next-urql'
import { Provider } from 'react-redux'
import { store } from 'store'
import ApplicationLayout from 'layouts/ApplicationLayout'
import { useEffect, useState } from 'react'
import Head from 'next/head'
import { DefaultSeo } from 'next-seo'
import { clientOptions } from 'helpers/client'
import CustomToaster from 'components/CustomToaster'

const App = ({ Component, pageProps }: AppProps) => {
  const [canRender, setCanRender] = useState(false)

  useEffect(() => {
    setCanRender(true)
  }, [])

  if (!canRender) {
    return <span></span>
  }

  return (
    <>
      <Head>
        <link rel="icon" type="image/png" href="/favicon.png" />
      </Head>

      <DefaultSeo
        titleTemplate="%s • ProConvey"
        defaultTitle="ProConvey"
        description="ProConvey is a property conveyancing software that helps conveyancers to manage their business and clients."
        openGraph={{
          title: 'ProConvey',
          description: 'ProConvey is a property conveyancing software that helps conveyancers to manage their business and clients.',
          type: 'website',
          locale: 'en_GB',
          url: 'https://www.proconvey.co.uk/',
          siteName: 'ProConvey',
        }}
      />

      <Provider store={store}>
        <ApplicationLayout>
          <Component {...pageProps} />
        </ApplicationLayout>
      </Provider>

      <CustomToaster />
    </>
  )
}

export default withUrqlClient(
  () => clientOptions,
)(App)
