import { ComponentStory, ComponentMeta } from '@storybook/react'
import Form from '../index'

export default {
  title: 'Components/Forms/Group',
  component: Form.Group,
} as ComponentMeta<typeof Form.Group>

export const TwoColumns: ComponentStory<typeof Form.Group> = () => {
  return (
    <Form.Group>
      <Form.Input placeholder="First Name" />
      <Form.Input placeholder="Last Name" />
    </Form.Group>
  )
}
