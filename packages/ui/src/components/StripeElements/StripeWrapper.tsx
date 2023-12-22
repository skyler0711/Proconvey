import { loadStripe } from '@stripe/stripe-js'
import { Elements } from '@stripe/react-stripe-js'
import { ReactNode, useMemo } from 'react'

type PropTypes = {
  children: ReactNode
  publishableKey: string
  accountId?: string
  awaitAccountId?: boolean
}

const StripeWrapper = ({ children, publishableKey, accountId, awaitAccountId = false }: PropTypes) => {
  const stripePromise = useMemo(() => {
    if (awaitAccountId && !accountId) {
      return null
    }
    return loadStripe(publishableKey, {
      stripeAccount: accountId,
    })
  }, [accountId, publishableKey, awaitAccountId])

  return (
    <Elements stripe={stripePromise}>
      {children}
    </Elements>
  )
}

export default StripeWrapper
