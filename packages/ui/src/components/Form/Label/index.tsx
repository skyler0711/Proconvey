import classNames from 'classnames'
interface PropTypes extends React.LabelHTMLAttributes<HTMLLabelElement> {
  children: React.ReactNode
}

const Label = ({ children, className, ...props }: PropTypes) => {
  return (
    <label {...props} className={classNames('block font-bold text-xl leading-[1.5625rem] mb-2.5', className)}>{children}</label>
  )
}

export default Label
