import Body from './Body'
import Cell from './Cell'
import Head from './Head'
import Row from './Row'

interface PropTypes extends React.TableHTMLAttributes<HTMLTableElement> {
  children: React.ReactNode
}

const DataEntryList = ({ children, ...props }: PropTypes) => {
  return (
    <div className="w-full overflow-x-auto">
      <table className="w-full border-separate border-spacing-y-3" {...props}>
        {children}
      </table>
    </div>
  )
}

DataEntryList.Head = Head
DataEntryList.Body = Body
DataEntryList.Row = Row
DataEntryList.Cell = Cell

export default DataEntryList
