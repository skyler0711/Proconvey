import Body from './Body'
import Cell from './Cell'
import Head from './Head'
import Row from './Row'

interface PropTypes extends React.TableHTMLAttributes<HTMLTableElement> {
  children: React.ReactNode
}

const Table = ({ children, ...props }: PropTypes) => {
  return (
    <div className="w-full overflow-x-auto">
      <table className="w-full border-seperate border-spacing-0" {...props}>
        {children}
      </table>
    </div>
  )
}

Table.Head = Head
Table.Body = Body
Table.Row = Row
Table.Cell = Cell

export default Table
