type PropTypes = {
  children: React.ReactNode
}

const Footer = ({ children }: PropTypes) => {
  return (
    <div className="flex justify-end gap-5 mt-auto">
      {children}
    </div>
  )
}

export default Footer
