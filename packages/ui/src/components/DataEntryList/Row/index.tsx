interface PropTypes extends React.TdHTMLAttributes<HTMLTableRowElement> {
  children: React.ReactNode
}

const Row = ({ children, ...props }: PropTypes) => {
  return (
    <tr {...props}>
      {children}
    </tr>
  )
}

export default Row
