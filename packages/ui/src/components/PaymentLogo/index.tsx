import { ReactNode } from 'react'
import AmericanExpress from './brands/AmericanExpress'
import DirectDebit from './brands/DirectDebit'
import Mastercard from './brands/Mastercard'
import Visa from './brands/Visa'

type PropTypes = {
  logo: 'direct_debit' | 'visa' | 'mastercard' | 'amex' | 'diners'
}

const Wrapper = ({ children }: { children?: ReactNode }) => {
  return (
    <div className="border border-primary-ring rounded-[0.375rem] w-[80px] h-[40px] flex items-center justify-center p-[10px]">
      {children}
    </div>
  )
}

const PaymentLogo = ({ logo }: PropTypes) => {
  switch (logo) {
    case 'direct_debit':
      return <Wrapper>
        <DirectDebit />
      </Wrapper>
    case 'visa':
      return <Wrapper>
        <Visa />
      </Wrapper>
    case 'mastercard':
      return <Wrapper>
        <Mastercard className="max-w-[42px]" />
      </Wrapper>
    case 'amex':
      return <Wrapper>
        <AmericanExpress />
      </Wrapper>
    default:
      return <Wrapper></Wrapper>
  }
}

export default PaymentLogo
