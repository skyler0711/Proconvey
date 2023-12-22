import { ComponentStory, ComponentMeta } from '@storybook/react'
import MultipleSelect from './index'

export default {
  title: 'Components/Forms/MultipleSelect',
  component: MultipleSelect,
} as ComponentMeta<typeof MultipleSelect>

const Template: ComponentStory<typeof MultipleSelect> = (args) => <MultipleSelect {...args} />

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

export const LabelledMultipleSelect: ComponentStory<typeof MultipleSelect> = Template.bind({})
LabelledMultipleSelect.args = {
  label: 'MultipleSelect Option',
  placeholder: 'Placeholder',
  options: options,
}

export const DisabledMultipleSelect: ComponentStory<typeof MultipleSelect> = Template.bind({})
DisabledMultipleSelect.args = {
  label: 'MultipleSelect Option',
  options: options,
  disabled: true,
  defaultValue: [{
    text: 'Option 1',
    value: '123',
  }],
}

export const ErrorMultipleSelect: ComponentStory<typeof MultipleSelect> = Template.bind({})
ErrorMultipleSelect.args = {
  label: 'MultipleSelect Option',
  placeholder: 'Placeholder',
  options: options,
  error: 'Please MultipleSelect an option',
}
