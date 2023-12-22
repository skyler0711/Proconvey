type PropTypes = {
  children: React.ReactNode
}

const Body = ({ children }: PropTypes) => {
  return (
    <tbody>
      {children}
    </tbody>
  )
}

export default Body
