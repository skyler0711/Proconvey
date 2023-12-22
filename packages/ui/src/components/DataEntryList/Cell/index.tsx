import classNames from 'classnames'
import { ElementType } from 'react'

interface PropTypes extends React.TdHTMLAttributes<HTMLTableCellElement> {
  children: React.ReactNode
  as?: ElementType
  customStyle?: string
  hasError?: boolean
}

const Cell = ({
  children,
  as: Component = 'td',
  align = 'left',
  hasError = false,
  customStyle = '',
  ...props
}: PropTypes) => {

  const innerClassName = classNames({
    [customStyle]: customStyle !== '',
    'py-3 px-5 text-base text-primary font-medium leading-5 first:rounded-l-[0.625rem] last:rounded-r-[0.625rem]': Component === 'th',
    'py-5 px-5 text-lg text-body first:font-bold leading-[1.375rem] border-b border-t first:border-l last:border-r first:rounded-l-[0.625rem] last:rounded-r-[0.625rem]': Component === 'td',


    'border-primary border-opacity-[15%]': Component === 'td' && !hasError,
    'border-danger': Component === 'td' && hasError,
  })

  return (
    <Component align={align} className={innerClassName} {...props}>
      {children}
    </Component>
  )
}

export default Cell
