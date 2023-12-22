import { H3 } from '../../Headers'

type PropTypes = {
  children: React.ReactNode
}

const ContentTitle = ({ children }: PropTypes) => {
  return (
    <H3>{children}</H3>
  )
}

export default ContentTitle
