import { ComponentStory, ComponentMeta } from '@storybook/react'
import Input from './index'

export default {
  title: 'Components/Forms/Input',
  component: Input,
} as ComponentMeta<typeof Input>

const Template: ComponentStory<typeof Input> = (args) => <Input {...args} />

export const LabelledInput: ComponentStory<typeof Input> = Template.bind({})
LabelledInput.args = {
  value: 'example@example.com',
  label: 'Email',
}

export const PlaceholderInput: ComponentStory<typeof Input> = Template.bind({})
PlaceholderInput.args = {
  placeholder: 'Email',
}

export const DisabledInput: ComponentStory<typeof Input> = Template.bind({})
DisabledInput.args = {
  label: 'Email',
  placeholder: 'Email',
  disabled: true,
}

export const HelpInput: ComponentStory<typeof Input> = Template.bind({})
HelpInput.args = {
  label: 'Password',
  placeholder: '••••••••',
  help: 'Must be a mix of at least 8 upper and lower case characters',
}

export const ErrorInput: ComponentStory<typeof Input> = Template.bind({})
ErrorInput.args = {
  label: 'Email',
  placeholder: 'Email',
  error: 'Please enter a valid email address',
}
