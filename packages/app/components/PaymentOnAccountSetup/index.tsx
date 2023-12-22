import Alert from '@proconvey/ui/src/components/Alert'
import Button from '@proconvey/ui/src/components/Button'
import Tag from '@proconvey/ui/src/components/Tag'
import { CheckmarkIcon, SlashCircleIcon } from '@proconvey/ui/src/icons'
import StripeLogo from 'components/StripeLogo'
import { graphql } from 'gql'
import { useEffect, useState } from 'react'
import { useMutation, useQuery } from 'urql'

type PropTypes = {
  stripeCode?: string
  onConnected?: () => void
  onDisconnected?: () => void
}

const PaymentOnAccountSetup = ({ stripeCode, onConnected, onDisconnected }: PropTypes) => {
  const [isConnected, setIsConnected] = useState(false)
  const [error, setError] = useState<string>()

  const redirectUrl = `https://connect.stripe.com/oauth/authorize?client_id=${process.env.NEXT_PUBLIC_STRIPE_CONNECT_CLIENT_ID}&response_type=code&scope=read_write&redirect_uri=${window.location.origin}${window.location.pathname}`

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query registrationPaymentsConveyancer {
        me {
          id
          conveyancer {
            id
            stripe_account_id
          }
        }
      }
    `),
  })

  const [{ fetching: fetchingUpdate }, updateStripeCode] = useMutation(graphql(`
    mutation updateStripeCode ($input: UpdateStripeCodeInput!) {
      updateStripeCode(input: $input)
    }
  `))

  const [{ fetching: fetchingDisconnect }, disconnectStripe] = useMutation(graphql(`
    mutation disconnectStripe {
      disconnectStripe
    }
  `))

  useEffect(() => {
    if (!isConnected && data?.me?.conveyancer?.stripe_account_id) {
      setIsConnected(true)
      onConnected?.()
    }
  }, [data, onConnected, isConnected])

  useEffect(() => {
    if (!isConnected && !error && stripeCode && !fetching && !fetchingUpdate) {
      updateStripeCode({
        input: {
          code: stripeCode as string,
        },
      }).then((response) => {
        if (response.error) {
          setError('There was an error connecting your Stripe account. Please try again.')
          return
        }
        setIsConnected(true)
        onConnected?.()
      })
    }
  }, [isConnected, stripeCode, updateStripeCode, fetching, error, fetchingUpdate, onConnected])

  const handleDisconnect = async () => {
    await disconnectStripe({})
    setIsConnected(false)
    setError(undefined)
    onDisconnected?.()
  }

  return (
    <div className="flex flex-col gap-[2.1875rem] items-center justify-center border rounded-lg border-outlined p-[1.75rem] mt-[1.25rem]">
      <div className="w-[11.75rem]">
        <StripeLogo />
      </div>

      {
        isConnected
          ? <>
            <Tag variant="success">
              <CheckmarkIcon className="w-4 h-4" />
              <p>Connected</p>
            </Tag>

            <Button variant="link" className="!text-danger" onClick={handleDisconnect} loading={fetchingDisconnect}>
              <SlashCircleIcon className="inline-block mr-2" /> Disconnect
            </Button>
          </>
          : (
            <>
              <a href={redirectUrl}>
                <Button size="small" loading={fetchingUpdate || fetching}>Setup</Button>
              </a>
              <p className="text-primary max-w-[50.0625rem] text-center">Connect your bank account instantly if you have a Stripe account, or create an account in less than 5 minutes to accept payments directly to your company bank account.</p>
            </>
          )
      }

      {
        error && (
          <Alert variant="danger">
            {error}
          </Alert>
        )
      }
    </div>
  )
}

export default PaymentOnAccountSetup
