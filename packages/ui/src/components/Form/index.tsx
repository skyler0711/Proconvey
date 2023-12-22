import { ElementType } from 'react'
import Checkbox from './Checkbox'
import Group from './Group'
import ImageUpload from './ImageUpload'
import Input from './Input'
import Select from './Select'
import MultipleSelect from './MultipleSelect'

interface PropTypes extends React.FormHTMLAttributes<HTMLFormElement> {
  children: React.ReactNode
  as?: ElementType
}

const Form = ({ children, as: Component = 'form', ...props }: PropTypes) => {
  return (
    <Component {...props} className="flex flex-col gap-5">
      {children}
    </Component>
  )
}

Form.Group = Group
Form.Input = Input
Form.Select = Select
Form.MultipleSelect = MultipleSelect
Form.Checkbox = Checkbox
Form.ImageUpload = ImageUpload

export default Form
