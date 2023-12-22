import Header from './Header'
import Body from './Body'
import classNames from 'classnames'
import Footer from '@proconvey/ui/src/components/Modals/Footer'

type PropTypes = {
  children: React.ReactNode
  className?: string
}

const Card = ({ children, className }: PropTypes) => {
  return (
    <div className={classNames('card-body flex flex-col bg-white border border-primary border-opacity-[14%] rounded-[0.625rem]', className)}>
      {children}
    </div>
  )
}

Card.Header = Header
Card.Body = Body
Card.Footer = Footer

export default Card
