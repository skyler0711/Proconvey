import { ComponentStory, ComponentMeta } from '@storybook/react'
import Alert from './index'

export default {
  title: 'Components/Alert',
  component: Alert,
} as ComponentMeta<typeof Alert>

const Template: ComponentStory<typeof Alert> = (args) => <Alert {...args} />

export const Danger: ComponentStory<typeof Alert> = Template.bind({})
Danger.args = {
  variant: 'danger',
  children: 'Incorrect email address. Please try again',
}
