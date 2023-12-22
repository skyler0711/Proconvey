import { ComponentStory, ComponentMeta } from '@storybook/react'
import Label from './index'

export default {
  title: 'Components/Forms/Label',
  component: Label,
} as ComponentMeta<typeof Label>

const Template: ComponentStory<typeof Label> = (args) => <Label {...args} />

export const FilledLabel: ComponentStory<typeof Label> = Template.bind({})
FilledLabel.args = {
  children: 'Example Label',
}
