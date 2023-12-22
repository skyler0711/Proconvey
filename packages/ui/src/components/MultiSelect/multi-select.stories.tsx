import { ComponentMeta, ComponentStory } from '@storybook/react'
import MultiSelect from '.'

export default {
  title: 'Components/MultiSelect',
  component: MultiSelect,
} as ComponentMeta<typeof MultiSelect>

const Template: ComponentStory<typeof MultiSelect> = (args) => <MultiSelect {...args} />

const options = [
  { id: '1', text: 'Mr John Dee' } ,
  { id: '2', text: 'Mrs Amy Newton' },
  { id: '3', text: 'Miss London Newham' },
  { id: '4', text: 'Mr Jeremy Clarkson' },

]

export const Example: ComponentStory<typeof MultiSelect> = Template.bind({})
Example.args = {
  label: 'Who are they representing?',
  placeholder: 'Select who they are representing',
  options: options,
  showSelection: false,
}
