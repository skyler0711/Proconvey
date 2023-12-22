import { ComponentMeta, ComponentStory } from '@storybook/react'
import { Textarea } from './index'

export default {
  title: 'Components/Textarea',
  component: Textarea,
} as ComponentMeta<typeof Textarea>

const Template: ComponentStory<typeof Textarea> = (args) => <Textarea {...args} />

export const TextAreaOnly = <Textarea />

export const TextArea: ComponentStory<typeof Textarea> = Template.bind({})
TextArea.args = {
  label: 'Text Area',
  placeholder: 'Please, provide details',
}

export const TextAreaWithoutPlaceholder: ComponentStory<typeof Textarea> = Template.bind({})
TextAreaWithoutPlaceholder.args = {
  label: 'Textarea without placeholder',
}

export const TextareaWithPlaceholderOnly: ComponentStory<typeof Textarea> = Template.bind({})
TextareaWithPlaceholderOnly.args = {
  placeholder: 'Please, provide details',
}
