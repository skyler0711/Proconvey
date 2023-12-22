import classNames from 'classnames'
import { ElementType } from 'react'

interface PropTypes extends React.TdHTMLAttributes<HTMLTableCellElement> {
  children?: React.ReactNode
  as?: ElementType
  className?: string
}

const Cell = ({ children, as: Component = 'td', align = 'left', className, ...props }: PropTypes) => {
  const innerClassName = classNames({
    'pt-2 pb-[0.6875rem] px-5 text-base font-normal text-primary text-opacity-80 leading-[1.375rem] border-b border-t border-primary border-opacity-[15%] border-primary border-opacity-[15%]': Component === 'th',
    'py-5 px-[1.1563rem] text-base text-body font-normal leading-[1.375rem] border-b border-t border-primary border-opacity-[15%] border-primary border-opacity-[15%]': Component === 'td',
  }, className)

  return (
    <Component align={align} className={innerClassName} {...props}>
      {children}
    </Component>
  )
}

export default Cell
