import { ComponentStory, ComponentMeta } from '@storybook/react'
import AddressFinder from './index'

export default {
  title: 'Components/AddressFinder',
  component: AddressFinder,
} as ComponentMeta<typeof AddressFinder>

const Template: ComponentStory<typeof AddressFinder> = (args) => <AddressFinder {...args} />

export const UnpopulatedAddress: ComponentStory<typeof AddressFinder> = Template.bind({})

export const ErrorAddress: ComponentStory<typeof AddressFinder> = Template.bind({})
ErrorAddress.args = {
  error: {
    line_1: { type: '', message: 'Line 1 error' },
    line_2: { type: '', message: 'Line 2 error' },
    city: { type: '', message: 'City error' },
    postcode: { type: '', message: 'Postcode error' },
  },
}
