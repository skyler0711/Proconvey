import Button from '@proconvey/ui/src/components/Button'
import { H3 } from '@proconvey/ui/src/components/Headers'
import { LocationIcon, ChevronDownIcon } from '@proconvey/ui/src/icons'
import { Property } from 'gql/graphql'
import Link from 'next/link'
import classNames from 'classnames'
import PropertyTag from '@proconvey/ui/src/components/PropertyTag'


type PropTypes = {
  property: {
    id: Property['id']
    users: {
      first_name?: Property['users'][0]['first_name']
      last_name?: Property['users'][0]['last_name']
    }[]
    address: Property['address']
    archived_at?: Property['archived_at']
    type: Property['type']
  }
}

const PropertyUserCard = ({ property }: PropTypes) => {

  const cardClassNames = classNames('p-5 w-full rounded-[0.625rem] bg-white border border-primary border-opacity-20 ', {
    'opacity-50': property.archived_at,
  })

  return (
    <div className={cardClassNames}>
      <div className="flex gap-5">
        <H3>{property?.users?.[0]?.first_name} {property?.users?.[0]?.last_name}</H3>
        {
          property.type &&
          <PropertyTag type={property.type}>{property.type}</PropertyTag>
        }
      </div>
      <div className="flex items-center gap-3 mt-[0.75rem] mb-[1.25rem]">
        <div className="w-[1.5rem]">
          <LocationIcon />
        </div>
        <p className="text-base text-body text-opacity-60">
          {property?.address?.line_1}
          {property?.address?.line_2 && `, ${property?.address?.line_2}`}
          {property?.address?.city && `, ${property?.address?.city}`}
          {property?.address?.postcode && `, ${property?.address?.postcode}`}
        </p>
      </div>

      <a
        href="/clients/notification"
      >
        <div className="flex items-center gap-1 mt-[1.25rem]">
          <button className="text-base text-primary">View all notifications</button>
          <ChevronDownIcon className="h-3 -rotate-90 text-primary" />
        </div>
      </a>

      <Link
        href={`/clients/${property.id}`}

      >
        <Button variant="primary" className="mt-[1.25rem]">View case</Button>
      </Link>
    </div>
  )
}

export default PropertyUserCard
