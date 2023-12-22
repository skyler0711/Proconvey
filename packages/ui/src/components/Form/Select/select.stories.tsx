import { ComponentStory, ComponentMeta } from '@storybook/react'
import Select from './index'

export default {
  title: 'Components/Forms/Select',
  component: Select,
} as ComponentMeta<typeof Select>

const Template: ComponentStory<typeof Select> = (args) => <Select {...args} />

const options = [
  {
    text: 'Option 1',
    value: '1',
  },
  {
    text: 'Option 2',
    value: '2',
  },
  {
    text: 'Option 3',
    value: '3',
  },
]

export const LabelledSelect: ComponentStory<typeof Select> = Template.bind({})
LabelledSelect.args = {
  label: 'Select Option',
  placeholder: 'Placeholder',
  options: options,
}

export const DisabledSelect: ComponentStory<typeof Select> = Template.bind({})
DisabledSelect.args = {
  label: 'Select Option',
  options: options,
  disabled: true,
  value: {
    text: 'Option 1',
    value: '123',
  },
}

export const ErrorSelect: ComponentStory<typeof Select> = Template.bind({})
ErrorSelect.args = {
  label: 'Select Option',
  placeholder: 'Placeholder',
  options: options,
  error: 'Please select an option',
}
